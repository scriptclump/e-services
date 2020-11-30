/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('expenses_api_call_details', {
    exp_call_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    exp_call_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    exp_call_from: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    exp_call_params: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    exp_call_response: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true
    }
  }, {
    tableName: 'expenses_api_call_details'
  });
};
