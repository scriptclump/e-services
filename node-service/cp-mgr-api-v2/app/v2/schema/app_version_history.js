/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('app_version_history', {
    version_history_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    version_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    version_pre_name: {
      type: DataTypes.STRING(50),
      allowNull: false
    },
    version_history_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    version_pre_number: {
      type: DataTypes.FLOAT,
      allowNull: false
    },
    version_history_number: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    app_type: {
      type: DataTypes.STRING(20),
      allowNull: false
    },
    pre_released_date: {
      type: DataTypes.DATE,
      allowNull: false
    },
    released_date: {
      type: DataTypes.DATE,
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
    tableName: 'app_version_history'
  });
};
