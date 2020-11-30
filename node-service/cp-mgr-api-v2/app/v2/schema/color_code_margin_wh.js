/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('color_code_margin_wh', {
    code_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    color_code: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    start_range: {
      type: DataTypes.DECIMAL,
      allowNull: false
    },
    end_range: {
      type: DataTypes.DECIMAL,
      allowNull: false
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'color_code_margin_wh'
  });
};
