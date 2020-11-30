/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('indent', {
    indent_id: {
      type: DataTypes.INTEGER(11).UNSIGNED,
      allowNull: false,
      primaryKey: true
    },
    indent_code: {
      type: DataTypes.STRING(16),
      allowNull: true,
      unique: true
    },
    indent_date: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    indent_type: {
      type: DataTypes.INTEGER(6),
      allowNull: true,
      defaultValue: '0'
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    currency_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '4'
    },
    indent_status: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '70001'
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_by: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'indent'
  });
};
