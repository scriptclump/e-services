/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('hr_policies', {
    policy_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    policy_name: {
      type: DataTypes.STRING(55),
      allowNull: true
    },
    hr_policy_id: {
      type: DataTypes.STRING(55),
      allowNull: true
    },
    effective_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    expire_date: {
      type: DataTypes.DATE,
      allowNull: true
    },
    file: {
      type: DataTypes.TEXT,
      allowNull: true
    }
  }, {
    tableName: 'hr_policies'
  });
};
