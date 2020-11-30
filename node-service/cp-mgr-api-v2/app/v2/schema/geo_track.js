/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('geo_track', {
    gt_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    user_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    geo_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    latitude: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    longitude: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    accuracy: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    speed: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    heading: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    reading: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    route_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    time_stamp: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'geo_track'
  });
};
