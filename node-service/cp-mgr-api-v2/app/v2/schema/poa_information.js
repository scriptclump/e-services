/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('poa_information', {
    record_index: {
      type: DataTypes.INTEGER(10),
      allowNull: false,
      primaryKey: true
    },
    activity_id: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    scheduled_start_date: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    scheduled_end_date: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    actual_start_date: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    actual_end_date: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    activity_description: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    activity_impact: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    activity_services: {
      type: DataTypes.STRING(1024),
      allowNull: true
    },
    activity_verification: {
      type: DataTypes.STRING(1024),
      allowNull: true
    },
    location: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    action: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    action_by: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    action_date: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    release_notes: {
      type: DataTypes.STRING(1024),
      allowNull: true
    }
  }, {
    tableName: 'poa_information'
  });
};
