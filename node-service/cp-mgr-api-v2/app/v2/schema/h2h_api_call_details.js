/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('h2h_api_call_details', {
    h2h_api_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    api_name: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    call_from: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    input_params: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    api_response: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true
    }
  }, {
    tableName: 'h2h_api_call_details'
  });
};
