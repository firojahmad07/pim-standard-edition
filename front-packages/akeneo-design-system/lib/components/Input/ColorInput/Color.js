"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.convertColorToLongHexColor = exports.isValidColor = void 0;
var convertColorToLongHexColor = function (value) {
    if (!isValidShortHexColor(value))
        return value;
    return "#" + value[1] + value[1] + value[2] + value[2] + value[3] + value[3];
};
exports.convertColorToLongHexColor = convertColorToLongHexColor;
var isValidShortHexColor = function (value) {
    return /^#[A-Fa-f0-9]{3}$/.test(value);
};
var isValidLongHexColor = function (value) {
    return /^#[A-Fa-f0-9]{6}$/.test(value);
};
var isValidColor = function (value) {
    return isValidLongHexColor(value) || isValidShortHexColor(value);
};
exports.isValidColor = isValidColor;
//# sourceMappingURL=Color.js.map