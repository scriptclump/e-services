/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('ecash_report', {
    ecash_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    emp_code: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    user_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    user_name: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    role_id: {
      type: DataTypes.INTEGER(5),
      allowNull: true
    },
    order_ecash: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00'
    },
    invoice_ecash: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00'
    },
    deliver_ecash: {
      type: DataTypes.DECIMAL,
      allowNull: true,
      defaultValue: '0.00'
    }
  }, {
    tableName: 'ecash_report'
  });
};
