/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('mfc_order_tracking', {
    mfc_id: {
      type: DataTypes.BIGINT,
      allowNull: false,
      primaryKey: true
    },
    order_code: {
      type: DataTypes.STRING(30),
      allowNull: false
    },
    user_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    status_id: {
      type: DataTypes.INTEGER(6),
      allowNull: false
    },
    approval_status: {
      type: DataTypes.INTEGER(1),
      allowNull: true
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
    tableName: 'mfc_order_tracking'
  });
};
