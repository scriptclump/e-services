/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('dynamic_cache_keys', {
    key_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    pattern: {
      type: DataTypes.STRING(100),
      allowNull: false
    },
    key_title: {
      type: DataTypes.STRING(200),
      allowNull: false
    },
    key: {
      type: DataTypes.STRING(500),
      allowNull: false
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'dynamic_cache_keys'
  });
};
