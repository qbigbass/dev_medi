/*
 Input Mask plugin extensions
 http://github.com/RobinHerbots/jquery.inputmask
 Copyright (c) 2010 -  Robin Herbots
 Licensed under the MIT license (http://www.opensource.org/licenses/mit-license.php)
 Version: 0.0.0-dev

 Russian Phone extension.
 */
(function (factory) {
	if (typeof define === "function" && define.amd) {
		define(["inputmask"], factory);
	} else if (typeof exports === "object") {
		module.exports = factory(require("./inputmask"));
	} else {
		factory(window.Inputmask);
	}
}
(function (Inputmask) {
	Inputmask.extendAliases({
		"phoneru": {
			alias: "abstractphone",
			countrycode: "7",
			phoneCodes: [

	{ "mask": "+7(###)###-##-##", "cc": "RU", "cd": "Russia", "type": "mobile" }
]
}
});

return Inputmask;
}));

