import SummarySkeleton from "./summaryskeleton";
import {fillValues, replaceTemplateVariables} from "../utils";

export default class Cis extends SummarySkeleton {

	static defaults = Object.assign({}, SummarySkeleton.defaults, {
		copyElement: null,

		total: 0,
		required: false,
		instances: [],
		internalInstances: [],
	})

	bind() {
		super.bind();
		this.handleCopyClick(true);
	}

	handleCopyClick(dir) {
		const copy = this.getElement('copy');

		if (copy == null) { return; }

		copy[dir ? 'addEventListener' : 'removeEventListener']('click', this.onCopyClick);
	}

	onCopyClick = (evt) => {
		this.copyInternal();
		evt.preventDefault();
	}

	updateTotal(count) {
		this.options.total = count;
		this.reflowStatus();
		this.reflowForm();
	}

	build() {
		const internalCises = this.optionValue('internal');

		return `<div class="yamarket-item-summary">
			${this.buildStatus()}
			${internalCises.length > 0 ? this.buildCopyIcon() : ''}
			<div class="yamarket-item-summary__modal" hidden>
				${this.buildForm()}
			</div>
		</div>`;
	}

	buildCopyIcon() {
		return `<button class="yamarket-item-summary__copy" type="button" title="${this.getMessage('COPY')}">
			${this.getMessage('COPY')}
		</button>`;
	}

	buildForm(useFormValue = false) {
		const total = parseInt(this.options.total) || 0;
		const iterator = (new Array(total)).fill(null);
		const value = useFormValue ? this.formValue() : this.optionValue();

		return `<div class="ui-form">
			${iterator.map((dummy, index) => {
				const one = value[index] || '';
				
				return `<div class="ui-form-row-inline">
					<div class="ui-form-row-inline-col">
						<div class="ui-form-label">
							<div class="ui-ctl-label-text">&numero;${index + 1}</div>
						</div>
					</div>
					<div class="ui-form-content">
						<div class="ui-ctl ui-ctl-sm ui-ctl-textbox ui-ctl-w100">
							<input class="ui-ctl-element" type="text" name="${this.options.name}[${index}]" value="${BX.util.htmlspecialchars(one)}" />
						</div>
					</div>
				</div>`;
			}).join('')}
		</div>`;
	}

	getStatus(value) {
		let result;

		if (value.length >= this.options.total) {
			result = SummarySkeleton.STATUS_READY;
		} else if (value.length > 0 || this.options.required) {
			result = SummarySkeleton.STATUS_WAIT;
		} else {
			result = SummarySkeleton.STATUS_EMPTY;
		}

		return result;
	}

	copyInternal(container = this.el) {
		const isChanged = fillValues(container, this.makeInternalValues());

		if (!isChanged) { return; }

		this.options.onChange && this.options.onChange();
		this.reflowStatus();
	}

	makeInternalValues() {
		const cises = this.optionValue('internal');
		const result = {};

		for (let index = 0; index < cises.length; ++index) {
			result[`${this.options.name}[${index}]`] = cises[index];
		}

		return result;
	}

	formValue() {
		const result = [];

		for (const input of this.fewElements('input')) {
			const value = input.value.trim();

			if (value === '') { continue; }

			result.push(value);
		}

		return result;
	}

	optionValue(optionKey = null) {
		const instances = optionKey != null ? this.options[optionKey + 'Instances'] : this.options.instances;

		return instances.map((instance) => instance['CIS']).filter((cis) => cis != null && cis.length > 0);
	}

	getMessage(key, replaces = null) {
		const keyWithPrefix = 'ITEM_CIS_' + key;
		const option = this.options.messages[keyWithPrefix];

		if (option != null) {
			return replaceTemplateVariables(option, replaces);
		}

		return super.getMessage(key, replaces);
	}

}