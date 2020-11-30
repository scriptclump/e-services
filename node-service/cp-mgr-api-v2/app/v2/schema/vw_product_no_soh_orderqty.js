/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_product_no_soh_orderqty', {
    Warehouse ID: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    Warehouse: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Product ID: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    SOH: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    ATP: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    Order Qty: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    Inv. Mode: {
      type: DataTypes.ENUM('atp+soh','atp','soh'),
      allowNull: false,
      defaultValue: 'soh'
    }
  }, {
    tableName: 'vw_product_no_soh_orderqty'
  });
};
