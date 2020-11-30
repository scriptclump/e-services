/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_Ordersgreaterthan10k', {
    gds_order_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    order_code: {
      type: DataTypes.STRING(16),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    Beat: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    hubname: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    shop_name: {
      type: DataTypes.STRING(75),
      allowNull: true
    },
    total: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    Order Status: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    FF: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    is_self: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    }
  }, {
    tableName: 'vw_Ordersgreaterthan10k'
  });
};
