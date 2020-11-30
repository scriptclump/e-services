/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('geo_track_history', {
    geo_track_history_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    hub_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    hub_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    de_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    de_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    distance: {
      type: DataTypes.FLOAT,
      allowNull: false,
      defaultValue: '0.00'
    },
    trip_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    start_reading: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    stop_reading: {
      type: DataTypes.DECIMAL,
      allowNull: true
    }
  }, {
    tableName: 'geo_track_history'
  });
};
