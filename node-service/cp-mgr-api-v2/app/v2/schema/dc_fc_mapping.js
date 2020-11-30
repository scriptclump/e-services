/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('dc_fc_mapping', {
    dc_fc_mapping_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    dc_le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    dc_le_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    fc_le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    fc_le_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    status: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    }
  }, {
    tableName: 'dc_fc_mapping'
  });
};
