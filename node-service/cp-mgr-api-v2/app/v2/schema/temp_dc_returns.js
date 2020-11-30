/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('temp_dc_returns', {
    order_code: {
      type: DataTypes.STRING(16),
      allowNull: true
    },
    reference_no: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    return_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    DC_approved_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    approved_by: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    product_title: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    sku: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    returned_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    }
  }, {
    tableName: 'temp_dc_returns'
  });
};
