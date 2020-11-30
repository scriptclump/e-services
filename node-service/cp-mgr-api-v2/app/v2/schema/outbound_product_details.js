/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('outbound_product_details', {
    outbound_product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    outbound_order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    wms_service_no: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    product_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    product_name: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    product_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    product_sku: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'outbound_product_details'
  });
};
