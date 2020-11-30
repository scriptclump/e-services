/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('hsn_invoice_tax_details', {
    hsn_tax_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    hsn_code: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    hsn_desc: {
      type: DataTypes.STRING(25000),
      allowNull: true
    },
    dc: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    month_date: {
      type: DataTypes.STRING(10),
      allowNull: true
    },
    inv_total_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    inv_with_tax: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    inv_without_tax: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    inv_cgst: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    inv_sgst: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    inv_igst: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    inv_utgst: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    state_name: {
      type: DataTypes.STRING(30),
      allowNull: true
    }
  }, {
    tableName: 'hsn_invoice_tax_details'
  });
};
