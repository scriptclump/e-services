/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('emp_rm', {
    emp_ep_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    rm_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    emp_rm_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    }
  }, {
    tableName: 'emp_rm'
  });
};
