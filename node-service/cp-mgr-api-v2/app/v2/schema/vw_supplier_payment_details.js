/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_supplier_payment_details', {
    Supplier_Name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Amount_Paid: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    Pay_Date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    }
  }, {
    tableName: 'vw_supplier_payment_details'
  });
};
