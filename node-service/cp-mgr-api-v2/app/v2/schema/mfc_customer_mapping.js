/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('mfc_customer_mapping', {
    cust_mfc_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    mfc_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    cust_le_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    credit_limit: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    is_active: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.TIME,
      allowNull: true
    },
    updated_at: {
      type: DataTypes.TIME,
      allowNull: true
    },
    created_by: {
      type: DataTypes.TIME,
      allowNull: true
    },
    updated_by: {
      type: DataTypes.TIME,
      allowNull: true
    }
  }, {
    tableName: 'mfc_customer_mapping'
  });
};
