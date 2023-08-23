import SummarySkeleton from "./summaryskeleton";
import {fillValues, replaceTemplateVariables} from "../utils";

export default class Cis extends SummarySkeleton {

	static defaults = Object.assign({}, SummarySkeleton.defaults, {
		copyElement: '.yamarket-item-summary__copy',
		inputElement: 'input, select',

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
			${internalCises['COUNT'] > 0 ? this.buildCopyIcon() : ''}
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
				const selfName = `ITEMS[${index}]`;
				const one = value[selfName] || '';
				
				return `<div class="ui-form-row-inline">
					<div class="ui-form-row-inline-col">
						<div class="ui-form-label">
							<div class="ui-ctl-label-text">&numero;${index + 1}</div>
						</div>
					</div>
					<div class="ui-form-content">
						<div class="ui-ctl ui-ctl-sm ui-ctl-textbox ui-ctl-w100">
							<input class="ui-ctl-element" type="text" name="${this.inputName(selfName)}" value="${BX.util.htmlspecialchars(one)}" data-name="${selfName}" />
						</div>
					</div>
				</div>`;
			}).join('')}
			<div class="ui-form-row">
				<div class="ui-form-label">
					<div class="ui-ctl-label-text">${this.getMessage('FORMAT')}</div>
				</div>
				<div class="ui-form-content">
					<div class="ui-ctl ui-ctl-after-icon ui-ctl-dropdown ui-ctl-w100">
						<div class="ui-ctl-after ui-ctl-icon-angle"></div>
						<select class="ui-ctl-element" name="${this.inputName('TYPE')}" data-name="TYPE">
							${['CIS', 'UIN'].map((typeVariant) => {				
								return `<option value="${typeVariant}" ${typeVariant === value['TYPE'] ? 'selected' : ''}>${this.getMessage(typeVariant)}</option>`;
							}).join('')}
						</select>
					</div>
				</div>
			</div>
		</div>`;
	}

	getStatus(value) {
		let result;

		if (value['COUNT'] >= this.options.total) {
			result = SummarySkeleton.STATUS_READY;
		} else if (value['COUNT'] > 0 || this.options.required) {
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

		for (const [key, value] of Object.entries(cises)) {
			result[this.inputName(key)] = value;
		}

		return result;
	}

	inputName(selfName: string) : string {
		const bracketPosition = selfName.indexOf('[');
		let result;

		if (bracketPosition > 0) {
			result =
				this.options.name
				+ `[${selfName.substring(0, bracketPosition)}]`
				+ `${selfName.substring(bracketPosition)}`
		} else {
			result = this.options.name + `[${selfName}]`;
		}

		return result;
	}

	formValue() {
		const result = {
			COUNT: 0,
		};

		for (const input of this.fewElements('input')) {
			const name = String(input.dataset.name).trim();
			const value = input.value.trim();

			if (name === '' || value === '') { continue; }

			result[name] = value;

			if (/^ITEMS\[/.test(name)) {
				++result['COUNT'];
			}
		}

		return result;
	}

	optionValue(optionKey = null) {
		const instances = optionKey != null ? this.options[optionKey + 'Instances'] : this.options.instances;
		const type = this.instancesType(instances);
		const result = {
			TYPE: type,
			COUNT: 0,
		};
		let itemIndex = 0;

		for (const instance of instances) {
			if (instance[type] != null && instance[type].length > 0) {
				result[`ITEMS[${itemIndex}]`] = instance[type];
				++result['COUNT'];
				++itemIndex;
			}
		}

		return result;
	}

	instancesType(instances: Array) : string {
		let result = null;

		for (const instance of instances) {
			for (const type of [ 'UIN', 'CIS' ]) {
				if (instance[type] != null && instance[type].length > 0) {
					result = type;
					break;
				}
			}

			if (result != null) { break; }
		}

		return result ?? 'CIS';
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