/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('ff_call_logs', {
    log_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    ff_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    user_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    activity: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    check_in: {
      type: DataTypes.DATE,
      allowNull: true
    },
    check_out: {
      type: DataTypes.DATE,
      allowNull: true
    },
    prod_call_time: {
      type: DataTypes.DATE,
      allowNull: true
    },
    check_in_lat: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    check_in_long: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    check_out_lat: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    check_out_long: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    longitude: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    latitude: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'ff_call_logs'
  });
};
