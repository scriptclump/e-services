/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('collections', {
    collection_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    customer_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    customer_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    collection_code: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    invoice_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    invoice_code: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    invoice_amount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    gds_order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      references: {
        model: 'gds_orders',
        key: 'gds_order_id'
      }
    },
    order_code: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    return_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    return_total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    return_code: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    cancel_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    cancel_total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    cancel_code: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    discount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    discount_amt: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    discount_type: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    collected_amount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    rounded_amount: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    collectable_amt: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_on: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    status: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    }
  }, {
    tableName: 'collections'
  });
};
