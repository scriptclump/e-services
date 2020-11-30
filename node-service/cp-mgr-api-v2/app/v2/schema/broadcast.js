/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('broadcast', {
    broadcast_id: {
      type: DataTypes.INTEGER(100),
      allowNull: false,
      primaryKey: true
    },
    broadcast_to: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    broadcast_subject: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    broadcast_msg: {
      type: "BLOB",
      allowNull: true
    },
    broadcast_date: {
      type: DataTypes.INTEGER(255),
      allowNull: true
    },
    username: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    attachment_path: {
      type: DataTypes.STRING(255),
      allowNull: true
    }
  }, {
    tableName: 'broadcast'
  });
};
