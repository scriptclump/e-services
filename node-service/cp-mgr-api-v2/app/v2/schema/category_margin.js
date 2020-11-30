/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('category_margin', {
    cm_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    category_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    dc_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    dc_category_margin: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    fc_category_margin: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    effective_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
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
    tableName: 'category_margin'
  });
};
