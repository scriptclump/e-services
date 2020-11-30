/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('beat_master', {
    bm_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    beat_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    beat_name: {
      type: DataTypes.STRING(5000),
      allowNull: false
    },
    beat_rm: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    beat_rm_name: {
      type: DataTypes.STRING(5000),
      allowNull: false
    },
    days: {
      type: DataTypes.STRING(5000),
      allowNull: false
    },
    spoke_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    spoke_name: {
      type: DataTypes.STRING(5000),
      allowNull: false
    },
    state_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    state_name: {
      type: DataTypes.STRING(5000),
      allowNull: false
    },
    dc_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    dc_name: {
      type: DataTypes.STRING(5000),
      allowNull: false
    },
    hub_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    hub_name: {
      type: DataTypes.STRING(5000),
      allowNull: false
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
    }
  }, {
    tableName: 'beat_master'
  });
};
