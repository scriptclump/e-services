/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('oos_report', {
    oos_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    pid: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    customer_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    retailer_legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    mobile_no: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    shop_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    product_name: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    sku: {
      type: DataTypes.STRING(25),
      allowNull: true
    },
    mrp: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    req_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    ordered_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    oos_qty: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    sale_loss: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATEONLY,
      allowNull: true
    }
  }, {
    tableName: 'oos_report'
  });
};
