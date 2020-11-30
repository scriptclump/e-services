/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('isost_ticket_type', {
    ticket_type_id: {
      type: DataTypes.INTEGER(10),
      allowNull: false,
      primaryKey: true
    },
    ticket_type: {
      type: DataTypes.STRING(30),
      allowNull: false
    }
  }, {
    tableName: 'isost_ticket_type'
  });
};
