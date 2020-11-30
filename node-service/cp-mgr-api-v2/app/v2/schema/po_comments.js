/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('po_comments', {
    po_id: {
      type: DataTypes.INTEGER(40),
      allowNull: false,
      primaryKey: true
    },
    comments: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'po_comments'
  });
};
