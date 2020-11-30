/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('emp_device_details', {
    device_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    device_fname: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    device_sname: {
      type: DataTypes.STRING(500),
      allowNull: true
    },
    device_direction: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    serial_no: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    device_ip: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'emp_device_details'
  });
};
