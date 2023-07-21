console.log("measoftMap init");
console.log(measoftMap);


var measoftObject;

function measoftMapInit()
{
    measoftMapInit = measoftMap.config({
        'mapBlock': 'measoftMapBlock',
        'client_id': '8',					// Сюда нужно указать код extra курьерской службы
        'mapSize': {						// Размер карты
            'width': '650',
            'height': '650'
        },
        'centerCoords': ['55.755814', '37.617635'],
        'showMapButton': '1',
        'showMapButtonCaption': 'Выбрать пункт самовывоза',
        'filter': {
            'acceptcard': 'YES',    // Можно добавлять acceptcash (принимают наличные), acceptcard (Принимают карты), acceptfitting (Есть примерка), acceptindividuals (Если вы-физ. лицо)
        },
        'allowedFilterParams': ['acceptcash', 'acceptcard', 'acceptfitting'],
    }).init();

}