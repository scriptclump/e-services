/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('outbound_details', {
    outbound_order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    seller_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    mp_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    outbound_type: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    order_status: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    outbound_status: {
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
    tableName: 'outbound_details'
  });
};
