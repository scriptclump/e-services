/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('pjp_pincode_area', {
    pjp_pincode_area_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    pjp_name: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    days: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    rm_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    spoke_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      defaultValue: '0'
    },
    default_pincode: {
      type: DataTypes.STRING(8),
      allowNull: true
    },
    pdp: {
      type: DataTypes.STRING(30),
      allowNull: true
    },
    pdp_slot: {
      type: DataTypes.INTEGER(11),
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
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'pjp_pincode_area'
  });
};
