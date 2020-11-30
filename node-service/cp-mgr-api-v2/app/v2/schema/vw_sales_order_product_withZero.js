/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_sales_order_product_withZero', {
    Order ID: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    Order Product ID: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    Product ID: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    Product Name: {
      type: DataTypes.STRING(128),
      allowNull: true
    },
    QTY: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    esu: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    Price: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00000'
    },
    unitcal: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    unit_price: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    Cost: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00000'
    },
    SKU: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    No of Units: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    Order Date: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    KVI: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    }
  }, {
    tableName: 'vw_sales_order_product_withZero'
  });
};
