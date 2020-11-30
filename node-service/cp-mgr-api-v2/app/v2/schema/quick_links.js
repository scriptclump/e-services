/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('quick_links', {
    link_id: {
      type: DataTypes.INTEGER(10),
      allowNull: false,
      primaryKey: true
    },
    link: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    link_name: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    link_priority: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    }
  }, {
    tableName: 'quick_links'
  });
};
