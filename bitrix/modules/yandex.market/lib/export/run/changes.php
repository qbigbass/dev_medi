<?php

namespace Yandex\Market\Export\Run;

use Bitrix\Main;
use Yandex\Market;

class Changes
{
	protected static $flushQueue = [];
	protected static $queueInitialized = false;

	public static function register($setupId, $entityType, $entityId)
	{
		static::addFlushQueue($setupId, $entityType, $entityId);
		static::initializeQueue();
	}

	public static function releaseAll($setupId)
	{
		Storage\ChangesTable::deleteBatch([
			'filter' => [
				'=SETUP_ID' => $setupId,
			]
		]);
	}

	public static function release($setupId, Main\Type\DateTime $dateTime)
	{
		Storage\ChangesTable::deleteBatch([
			'filter' => [
				'=SETUP_ID' => $setupId,
				'<=TIMESTAMP_X' => Market\Data\DateTime::toCanonical($dateTime),
			]
		]);
	}

	public static function flush()
	{
		$chunkSize = static::getFlushChunkSize();

		foreach (array_chunk(static::$flushQueue, $chunkSize) as $flushChunk)
		{
			$flushChunk = static::markChangesTimestamp($flushChunk);

			Market\Export\Run\Storage\ChangesTable::addBatch($flushChunk, []);
		}

		static::$flushQueue = [];
		static::registerAgent();
	}

	protected static function addFlushQueue($setupId, $entityType, $entityId)
	{
		$key = $setupId . ':' . $entityType . ':' . $entityId;

		if (isset(static::$flushQueue[$key])) { return; }

		$limit = Market\Config::getOption('export_run_agent_changes_limit', 1000);

		static::$flushQueue[$key] = [
			'SETUP_ID' => $setupId,
			'ENTITY_TYPE' => $entityType,
			'ENTITY_ID' => $entityId,
		];

		if (count(static::$flushQueue) >= $limit)
		{
			static::flush();
		}
	}

	protected static function getFlushChunkSize()
	{
		return max(1, (int)Market\Config::getOption('changes_flush_chunk_size', 100));
	}

	protected static function markChangesTimestamp($changes)
	{
		$result = $changes;
		$dateTime = new Market\Data\Type\CanonicalDateTime();

		foreach ($result as &$change)
		{
			$change['TIMESTAMP_X'] = $dateTime;
		}
		unset($change);

		return $result;
	}

	protected static function initializeQueue()
	{
		if (static::$queueInitialized) { return; }

		static::$queueInitialized = true;
		static::bindFlush();
	}

	protected static function bindFlush()
	{
		$eventManager = Main\EventManager::getInstance();

		$eventManager->addEventHandler('main', 'OnAfterEpilog', [__CLASS__, 'flush']);
		register_shutdown_function([__CLASS__, 'flush']);
	}

	protected static function registerAgent()
	{
		Agent::register([
			'method' => 'change',
			'sort' => 200 // more priority
		]);
	}
}