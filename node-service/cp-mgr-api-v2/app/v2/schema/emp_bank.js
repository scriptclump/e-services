/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('emp_bank', {
    emp_bank_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    emp_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    ep_emp_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    acc_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    bank_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    branch_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    acc_type: {
      type: DataTypes.STRING(10),
      allowNull: true
    },
    acc_no: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    ifsc_code: {
      type: DataTypes.STRING(11),
      allowNull: true
    },
    micr_code: {
      type: DataTypes.STRING(12),
      allowNull: true
    },
    currency_code: {
      type: DataTypes.INTEGER(5),
      allowNull: true
    }
  }, {
    tableName: 'emp_bank'
  });
};
