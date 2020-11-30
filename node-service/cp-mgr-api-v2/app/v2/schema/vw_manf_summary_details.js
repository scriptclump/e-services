/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_manf_summary_details', {
    manufacturer_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    logo: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    user_name: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    contact: {
      type: DataTypes.STRING(51),
      allowNull: true
    },
    rel_manager: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    brands: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    products: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    documents: {
      type: DataTypes.BIGINT,
      allowNull: false,
      defaultValue: '0'
    },
    created_by: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'vw_manf_summary_details'
  });
};
