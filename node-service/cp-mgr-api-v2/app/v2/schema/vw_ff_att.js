/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_ff_att', {
    first_checkin_time: {
      type: DataTypes.DATE,
      allowNull: true
    },
    last_checkout_time: {
      type: DataTypes.DATE,
      allowNull: true
    },
    user_name: {
      type: DataTypes.STRING(51),
      allowNull: false,
      defaultValue: ''
    },
    role_id: {
      type: DataTypes.STRING(255),
      allowNull: true
    }
  }, {
    tableName: 'vw_ff_att'
  });
};
