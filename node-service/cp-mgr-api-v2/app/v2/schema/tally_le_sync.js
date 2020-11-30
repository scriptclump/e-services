/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('tally_le_sync', {
    sync_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    cost_centre: {
      type: DataTypes.STRING(150),
      allowNull: true
    },
    cost_centre_group: {
      type: DataTypes.STRING(150),
      allowNull: true
    },
    sync_url: {
      type: DataTypes.STRING(300),
      allowNull: true
    },
    is_active: {
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
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    updated_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    }
  }, {
    tableName: 'tally_le_sync'
  });
};
