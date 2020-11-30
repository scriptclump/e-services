/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('user_ecash_credit_details', {
    user_ecash_details_id: {
      type: DataTypes.INTEGER(15),
      allowNull: false,
      primaryKey: true
    },
    user_ecash_id: {
      type: DataTypes.INTEGER(15),
      allowNull: true
    },
    from_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    to_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    user_id: {
      type: DataTypes.INTEGER(15),
      allowNull: true
    },
    le_id: {
      type: DataTypes.INTEGER(15),
      allowNull: true
    },
    amount_requested_to_approve: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    status: {
      type: DataTypes.ENUM('1','0'),
      allowNull: true,
      defaultValue: '0'
    },
    created_by: {
      type: DataTypes.INTEGER(15),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true
    },
    updated_by: {
      type: DataTypes.INTEGER(15),
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true
    },
    updated_status: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    description: {
      type: DataTypes.STRING(250),
      allowNull: true
    }
  }, {
    tableName: 'user_ecash_credit_details'
  });
};
