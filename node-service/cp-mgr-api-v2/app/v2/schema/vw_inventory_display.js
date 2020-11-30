/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_inventory_display', {
    Product ID: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    Product Title: {
      type: DataTypes.STRING(2000),
      allowNull: true
    },
    SKU: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    Warehouse Name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Product Group ID: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    Pack_Type: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    HSN Code: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    KVI: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    MRP: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00000'
    },
    Is Sellable: {
      type: DataTypes.INTEGER(1),
      allowNull: true
    },
    CP Enabled: {
      type: DataTypes.STRING(3),
      allowNull: true
    },
    ESU: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    ESP: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    ELP: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    DLP: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    FLP: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    PTR: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0'
    },
    TAX: {
      type: DataTypes.STRING(12),
      allowNull: true
    },
    SOH: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    Return Pending Qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    Orders On Hand: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    Available Inventory: {
      type: DataTypes.BIGINT,
      allowNull: true
    },
    CFC Qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    Available CFC: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    Damage Qty: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    Missing Qty: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    Quarantine Qty: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    Freebie Desc: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    Freebie SKU: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    Offer Pack: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    Category: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    Manufacturer: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    Brand: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    Product Class: {
      type: DataTypes.STRING(2000),
      allowNull: true
    },
    Sub Category: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    Last PO Date: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    }
  }, {
    tableName: 'vw_inventory_display'
  });
};
