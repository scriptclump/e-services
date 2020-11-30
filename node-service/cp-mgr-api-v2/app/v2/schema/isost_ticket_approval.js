/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('isost_ticket_approval', {
    approval_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    ticket_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    request_date: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    requested_by: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    approval_by: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    approval_date: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    approval_comments: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    approval_key: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    approval_status: {
      type: DataTypes.STRING(20),
      allowNull: true,
      defaultValue: 'PENDING'
    }
  }, {
    tableName: 'isost_ticket_approval'
  });
};
