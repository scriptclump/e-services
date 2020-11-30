/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('business_groups', {
    business_group_index: {
      type: DataTypes.INTEGER(10),
      allowNull: false,
      primaryKey: true
    },
    business_group: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    prefix: {
      type: DataTypes.STRING(10),
      allowNull: true
    }
  }, {
    tableName: 'business_groups'
  });
};
