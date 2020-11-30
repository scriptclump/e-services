/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_NonSellableSohProducts', {
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    product_title: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    sku: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    kvi: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    soh: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    esu: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    esp: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    elp: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    ptrvalue: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00000'
    },
    is_sellable: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    product_class_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    brand_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    manufacturer_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    mrp: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00000'
    },
    cp_enabled: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    available_inventory: {
      type: DataTypes.BIGINT,
      allowNull: true
    }
  }, {
    tableName: 'vw_NonSellableSohProducts'
  });
};
