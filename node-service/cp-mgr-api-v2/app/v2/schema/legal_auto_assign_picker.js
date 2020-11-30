/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('legal_auto_assign_picker', {
    auto_assign_id: {
      type: DataTypes.INTEGER(10),
      allowNull: false,
      primaryKey: true
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    is_enable: {
      type: DataTypes.INTEGER(1),
      allowNull: true,
      defaultValue: '0'
    },
    roles_to_ignore: {
      type: DataTypes.STRING(250),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    created_by: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    }
  }, {
    tableName: 'legal_auto_assign_picker'
  });
};
