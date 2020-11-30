/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('sponsor_history_details', {
    history_id: {
      type: DataTypes.BIGINT,
      allowNull: false,
      primaryKey: true
    },
    config_mapping_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    config_object_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    config_object_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    user_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    hub_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    beat_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    action_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    cost: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    converted_to: {
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
    }
  }, {
    tableName: 'sponsor_history_details'
  });
};
