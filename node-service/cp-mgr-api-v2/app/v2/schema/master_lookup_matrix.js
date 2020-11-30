/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('master_lookup_matrix', {
    matrix_id: {
      type: DataTypes.INTEGER(5).UNSIGNED,
      allowNull: false,
      primaryKey: true
    },
    mas_cat_id: {
      type: DataTypes.INTEGER(5),
      allowNull: true
    },
    master_lookup_value: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    has_next_status_value: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    sms: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    email: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    }
  }, {
    tableName: 'master_lookup_matrix'
  });
};
