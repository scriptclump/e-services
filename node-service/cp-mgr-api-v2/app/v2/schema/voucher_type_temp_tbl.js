/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('voucher_type_temp_tbl', {
    gds_order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    invoice_code_new: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    invoice_code_old: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    order_code_new: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    order_code_old: {
      type: DataTypes.STRING(50),
      allowNull: true
    }
  }, {
    tableName: 'voucher_type_temp_tbl'
  });
};
