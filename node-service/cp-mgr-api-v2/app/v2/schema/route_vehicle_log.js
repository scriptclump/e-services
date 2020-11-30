/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('route_vehicle_log', {
    id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    vehicle: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    route_admin_log_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    assigned_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    estimate_trip_distance: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    actual_trip_distance: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    estimate_trip_time: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    actual_trip_time: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    created_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    trip_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    hub_id: {
      type: DataTypes.DATE,
      allowNull: true
    }
  }, {
    tableName: 'route_vehicle_log'
  });
};
