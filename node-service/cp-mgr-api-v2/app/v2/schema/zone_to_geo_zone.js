/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('zone_to_geo_zone', {
    zone_to_geo_zone_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    country_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    zone_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    geo_zone_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
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
    tableName: 'zone_to_geo_zone'
  });
};
