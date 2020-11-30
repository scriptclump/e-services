/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('stock_sept_27', {
    product_title: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    sku: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    mrp: {
      type: DataTypes.DECIMAL,
      allowNull: false,
      defaultValue: '0.00000'
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    old_soh: {
      type: DataTypes.BIGINT,
      allowNull: false,
      defaultValue: '0'
    },
    grn: {
      type: DataTypes.BIGINT,
      allowNull: false,
      defaultValue: '0'
    },
    total: {
      type: DataTypes.BIGINT,
      allowNull: false,
      defaultValue: '0'
    },
    invoiced_qty: {
      type: DataTypes.BIGINT,
      allowNull: false,
      defaultValue: '0'
    },
    cuurent_soh: {
      type: DataTypes.BIGINT,
      allowNull: false,
      defaultValue: '0'
    }
  }, {
    tableName: 'stock_sept_27'
  });
};
