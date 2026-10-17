import inventoryUnitEnum from "../enums/modules/inventoryUnitEnum";

// unit => [base unit its stock is kept in, how many base units one of it is] (same as InventoryUnitLibrary.php)
const units = {
    [inventoryUnitEnum.KG]: [inventoryUnitEnum.G, 1000],
    [inventoryUnitEnum.G]: [inventoryUnitEnum.G, 1],
    [inventoryUnitEnum.L]: [inventoryUnitEnum.ML, 1000],
    [inventoryUnitEnum.ML]: [inventoryUnitEnum.ML, 1],
    [inventoryUnitEnum.PCS]: [inventoryUnitEnum.PCS, 1],
    [inventoryUnitEnum.DOZEN]: [inventoryUnitEnum.PCS, 12]
};

const labels = {
    [inventoryUnitEnum.KG]: "kg",
    [inventoryUnitEnum.G]: "g",
    [inventoryUnitEnum.L]: "L",
    [inventoryUnitEnum.ML]: "ml",
    [inventoryUnitEnum.PCS]: "pcs",
    [inventoryUnitEnum.DOZEN]: "dozen"
};

export default {
    units: Object.keys(units),
    baseUnit: function (unit) {
        return units[unit] ? units[unit][0] : null;
    },
    factor: function (unit) {
        return units[unit] ? units[unit][1] : 1;
    },
    label: function (unit) {
        return labels[unit] || unit || "";
    }
};
