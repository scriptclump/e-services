/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('error_tbl', {
    error_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    error_code: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    error_desc: {
      type: DataTypes.STRING(5000),
      allowNull: true
    },
    full_error: {
      type: DataTypes.STRING(5000),
      allowNull: true
    },
    process_id: {
      type: DataTypes.STRING(5000),
      allowNull: false
    },
    util_id: {
      type: DataTypes.TEXT,
      allowNull: true
    }
  }, {
    tableName: 'error_tbl'
  });
};
