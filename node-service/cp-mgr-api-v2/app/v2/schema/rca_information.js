/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('rca_information', {
    record_index: {
      type: DataTypes.INTEGER(10),
      allowNull: false,
      primaryKey: true
    },
    activity_id: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    open_date: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    attended_date: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    closure_date: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    description: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    symptoms: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    impact_analysis: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    ca_root_cause: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    ca_reason: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    ca_action: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    pa_action: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    recommendations: {
      type: DataTypes.STRING(1000),
      allowNull: true
    },
    observations: {
      type: DataTypes.STRING(1000),
      allowNull: true
    }
  }, {
    tableName: 'rca_information'
  });
};
