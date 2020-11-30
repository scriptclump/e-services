/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('CancelledOrdersbylineitem', {
    gds_order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    order_date: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    order_code: {
      type: DataTypes.STRING(16),
      allowNull: true
    },
    le_code: {
      type: DataTypes.STRING(16),
      allowNull: true
    },
    Order Item Total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    Cancel Item Total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    sku: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    pname: {
      type: DataTypes.STRING(128),
      allowNull: true
    },
    Line Status: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    Cancel Reason: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    comments: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    Beat: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    Area: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    Hub: {
      type: DataTypes.STRING(255),
      allowNull: true
    }
  }, {
    tableName: 'CancelledOrdersbylineitem'
  });
};
