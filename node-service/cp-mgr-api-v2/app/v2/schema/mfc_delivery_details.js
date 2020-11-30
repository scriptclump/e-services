/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('mfc_delivery_details', {
    mfc_delivery_id: {
      type: DataTypes.BIGINT,
      allowNull: false,
      primaryKey: true
    },
    order_code: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    loan_amount: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    user_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    is_delivered: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'mfc_delivery_details'
  });
};
